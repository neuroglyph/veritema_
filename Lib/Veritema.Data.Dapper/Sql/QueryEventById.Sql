	SELECT
	[Id],
	[Start],
	[End],
	[Details],
	[Title],
	[LocationId],
	[StyleId],
	[Confirmed],
	[Updated],
	[TypeId]
FROM
	[v].[Event]
WHERE
	Id = @id