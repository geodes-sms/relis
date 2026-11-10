"""Compare two captured integration reports; never convert a failing suite to green."""
import argparse
from collections import defaultdict, deque
import json
from pathlib import Path

IDENTITY = ('test_controller', 'test_action', 'test_name', 'test_aspect')


def read_report(path):
    report = json.loads(path.read_text(encoding='utf-8'))
    rows = report['results']
    if not rows or report['total'] != len(rows):
        raise ValueError('Empty or inconsistent assertion totals: ' + str(path))
    passed = sum(row['result'] == 'passed' for row in rows)
    failed = sum(row['result'] == 'failed' for row in rows)
    if passed != report['passed'] or failed != report['failed'] or passed + failed != len(rows):
        raise ValueError('Inconsistent assertion outcomes: ' + str(path))
    return report


def compare(reference_path, candidate_path):
    reference, candidate = map(read_report, (reference_path, candidate_path))
    reference_ids = [tuple(row.get(k) for k in IDENTITY) for row in reference['results']]
    candidate_ids = [tuple(row.get(k) for k in IDENTITY) for row in candidate['results']]
    same_assertions = reference_ids == candidate_ids
    # New regressions may add assertions between existing ones. Match every
    # original occurrence and preserve its relative order; additions do not
    # excuse dropped or reordered reference assertions.
    available = defaultdict(deque)
    for index, identity in enumerate(candidate_ids):
        available[identity].append(index)
    matches, missing = [], []
    for before, identity in zip(reference['results'], reference_ids):
        if available[identity]:
            index = available[identity].popleft()
            matches.append((before, candidate['results'][index], index))
        else:
            missing.append({key: before.get(key) for key in IDENTITY})
    positions = [index for _, _, index in matches]
    order_preserved = positions == sorted(positions)
    used = set(positions)
    additions = [row for index, row in enumerate(candidate['results']) if index not in used]
    regressions, improvements = [], []
    for before, after, _ in matches:
        identity = {key: after.get(key) for key in IDENTITY}
        if before['result'] == 'passed' and after['result'] == 'failed':
            regressions.append(identity)
        elif before['result'] == 'failed' and after['result'] == 'passed':
            improvements.append(identity)
    regressions.extend({key: row.get(key) for key in IDENTITY} for row in additions if row['result'] == 'failed')
    report = {
        'same_assertion_order_and_identity': same_assertions,
        'preserves_reference_assertions': not missing and order_preserved,
        'matched_reference_assertions': len(matches),
        'missing_assertions': missing,
        'reference_order_preserved': order_preserved,
        'added_assertions': [{key: row.get(key) for key in IDENTITY} for row in additions],
        'reference': {k: reference[k] for k in ('php_version', 'database_version', 'total', 'passed', 'failed')},
        'candidate': {k: candidate[k] for k in ('php_version', 'database_version', 'total', 'passed', 'failed')},
        'new_failures': regressions, 'resolved_failures': improvements,
        'reference_diagnostic_bytes': len(reference.get('diagnostics', '').encode()),
        'candidate_diagnostic_bytes': len(candidate.get('diagnostics', '').encode()),
        'candidate_logged_php_diagnostic_count': candidate.get('logged_php_diagnostic_count'),
        'candidate_logged_query_error_count': candidate.get('logged_query_error_count'),
        'candidate_suite_passes': candidate['failed'] == 0 and candidate['status'] == 'successful',
    }
    return report


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('reference', type=Path)
    parser.add_argument('candidate', type=Path)
    parser.add_argument('--output', type=Path)
    args = parser.parse_args()
    report = compare(args.reference, args.candidate)
    encoded = json.dumps(report, indent=2) + '\n'
    if args.output:
        args.output.write_text(encoded, encoding='utf-8')
    print(encoded, end='')
    # A comparison is evidence, not a waiver for inherited failures or diagnostics.
    raise SystemExit(0 if report['preserves_reference_assertions'] and
                     report['candidate_suite_passes'] and not report['new_failures'] and
                     report['candidate_diagnostic_bytes'] == 0 and
                     report['candidate_logged_php_diagnostic_count'] == 0 and
                     report['candidate_logged_query_error_count'] == 0 else 1)
