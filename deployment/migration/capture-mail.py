"""Local-only SMTP sink for the captured Python 3.7 editor image; never delivers mail."""
import asyncore
import smtpd
import uuid
from pathlib import Path


class Capture(smtpd.SMTPServer):
    def process_message(self, peer, mailfrom, rcpttos, data, **kwargs):
        Path('/mail').mkdir(exist_ok=True)
        Path('/mail', uuid.uuid4().hex + '.eml').write_bytes(data)


if __name__ == '__main__':
    Capture(('0.0.0.0', 1025), None, decode_data=False)
    asyncore.loop()
