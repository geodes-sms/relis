SELECT D.decison_id,D.screening_decision,D.screening_phase,P.id, P.bibtexKey,P.title from screen_decison D LEFT JOIN paper P ON(D.paper_id=P.id AND P.paper_active=1 )	WHERE decision_active=1


SELECT D.decison_id,D.screening_decision as screening_status,D.screening_phase,P.id, P.bibtexKey,P.title,P.paper_active 
			from screen_decison D LEFT JOIN paper P ON(D.paper_id=P.id AND P.paper_active=1 )	
			WHERE decision_active=1
			
			
SELECT S.screening_id,S.screening_phase,P.id, P.bibtexKey,P.title,P.paper_active,D.screening_decision as screening_status from screening_paper S LEFT JOIN  paper P ON(S.paper_id=P.id AND P.paper_active=1 ) LEFT JOIN  screen_decison D ON (P.id=D.paper_id AND D.decision_active=1 )  WHERE screening_active=1 AND S.screening_phase=1

SELECT S.screening_id,S.screening_phase,P.id, P.bibtexKey,P.title,P.paper_active,IFNULL(D.screening_decision,'Pending') as screening_status from screening_paper S 
LEFT JOIN  paper P ON(S.paper_id=P.id AND P.paper_active=1 ) 
LEFT JOIN  screen_decison D ON (P.id=D.paper_id AND D.decision_active=1 ) 
WHERE screening_active=1  GROUP BY P.id