MERGE INTO [v].[Event] as T
USING
(
	VALUES
	(@id,@title,@start, @end,@details,@confirmed,@styleId,@locationId,@updated,@typeId)
)
AS
	S
	(
		[Id]
		,[Title]
		,[Start]
		,[End]
		,Details
		,Confirmed
		,StyleId
		,LocationId
		,[Updated]
		,[TypeId]
	)
ON
	T.Id = S.Id
WHEN MATCHED THEN
UPDATE SET
	T.[Start] = S.[Start]
	,T.[Title]= S.[Title]
	,T.[End] = S.[End]
	,T.[Details] = S.[Details]
	,T.[Confirmed] = S.[Confirmed]
	,T.[StyleId] = S.[StyleId]
	,T.[LocationId] = S.[LocationId]
	,T.[Updated] = S.[Updated]
	,T.[TypeId] = S.[TypeId]
WHEN NOT MATCHED BY TARGET THEN
	INSERT ([Title],[Start],[End],Details,Confirmed,StyleId,LocationId,[Updated],[TypeId])
	VALUES ([Title],[Start],[End],Details,Confirmed,StyleId,LocationId,[Updated],[TypeId])
OUTPUT Inserted.*;