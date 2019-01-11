MERGE INTO [v].[MartialStyle] as T
USING
(
	VALUES
	(1, 'Hapkido'),
	(2, 'Judo'),
	(3, 'JiuJitsu'),
	(4, 'Muay Thai'),
	(5, 'Tai Chi')
)
AS
	S
	(
		Id
		,Display
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id,Display)
	VALUES (Id,Display)
WHEN MATCHED THEN
UPDATE SET
	T.[Display] = S.[Display]
WHEN NOT MATCHED BY SOURCE THEN
DELETE;