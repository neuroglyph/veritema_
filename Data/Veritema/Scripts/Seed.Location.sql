print 'Executing Script Seed.Location.sql'

SET IDENTITY_INSERT [v].[Location] ON

MERGE INTO [v].Location as T
USING
(
	VALUES
	(1, 'RIMA Central','{ "Contacts":["tel:9196017534","https://veritema.azurewebsites.net","tel:9198284447"], "Street":"601 East Six Forks Road", "Street2":"Suite 100", "City":"Raleigh", "State":"NC", "Zip":"27607"}'),
	(2, 'Cape Fear Martial Arts','{"Contacts": ["tel:9106862678"], "Street":"6832 Market St","City":"Wilmington", "State":"NC","Zip":"28405"}')
)
AS
	S
	(
		Id
		,Name
		,[Value]
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id,Name,[Value],Created,Updated)
	VALUES (Id,Name,[Value],SYSDATETIMEOFFSET(),SYSDATETIMEOFFSET())
WHEN MATCHED THEN
UPDATE SET
	T.[Name] = S.[Name]
	,T.[Value] = S.[Value]
	,T.[Updated] = SYSDATETIMEOFFSET()
WHEN NOT MATCHED BY SOURCE THEN
DELETE;

SET IDENTITY_INSERT [v].[Location] OFF

print 'Completed Script Seed.Location.sql'
print '-------------------------------------------------'