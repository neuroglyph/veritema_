SELECT
	Id,
	[Start],
	[End],
	[Details],
	[Title],
	[StyleId],
	[LocationId],
	[Confirmed],
	[Updated],
	[TypeId]
FROM
	[v].[Event]
WHERE
	(@from is null OR  [Start] >= @from) AND
	(@to is null OR [End] <= @to)
