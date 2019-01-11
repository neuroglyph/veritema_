print 'Executing Script Seed.EventType.sql'

MERGE INTO [v].EventType as T
USING
(
	VALUES
	('C', 'Class','A group based reocurring session based upon a cirriculum'),
	('P', 'Private Lesson','One on One instruction with the Master'),
	('S', 'Seminar', 'A group session focus on the application of a few techniques')
)
AS
	S
	(
		Id
		,Name
		,[Description]
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id,Name,[Description])
	VALUES (Id,Name,[Description])
WHEN MATCHED THEN
UPDATE SET
	T.[Name] = S.[Name]
	,T.[Description] = S.[Description]
WHEN NOT MATCHED BY SOURCE THEN
DELETE;

print 'Completed Script Seed.EventType.sql'
print '-------------------------------------------------'