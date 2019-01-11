SELECT
	[Id]
	,[Name]
	,JSON_VALUE([Value],'$.Street') as [Street]
	,JSON_VALUE([Value],'$.Street2') as [Street2]
	,JSON_VALUE([Value],'$.City') as [City]
	,JSON_VALUE([Value],'$.State') as [State]
	,JSON_VALUE([Value],'$.Zip') as [Zip]
	,JSON_QUERY([Value],'$.Contacts') as [Contacts]
FROM
	[v].[Location]