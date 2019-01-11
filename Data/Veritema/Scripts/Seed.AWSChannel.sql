print 'Executing Script Seed.AwsChannel.sql'

MERGE INTO [v].AwsChannel as T
USING
(
	VALUES

	('1', null, 'ContactUsForm','arn:aws:sns:us-east-1:963663741849:ContactUsForm', 'T', '', ''),
	('2', null, 'PrivateLessons','arn:aws:sns:us-east-1:963663741849:PrivateLessons', 'T', '', '')
)
AS
	S
	(
		Id
		,ParentId
		,Name 
		,ARN  
		,ChannelType  
		,Protocol 
		,[EndPoint]
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id, ParentId, Name, ARN, ChannelType, Protocol, EndPoint)
	VALUES (Id, ParentId, Name, ARN, ChannelType, Protocol, EndPoint)
WHEN MATCHED THEN
UPDATE SET
	T.ParentId = S.ParentId
	 ,T.[Name] = S.[Name]
	 ,T.ARN = S.ARN
	 ,T.ChannelType = S.ChannelType
	 ,T.Protocol = S.Protocol
	 ,T.[EndPoint] = S.[EndPoint]
WHEN NOT MATCHED BY SOURCE THEN
DELETE;

print 'Completed Script Seed.EventType.sql'
print '-------------------------------------------------'