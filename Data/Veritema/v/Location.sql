CREATE TABLE [v].[Location]
(
	[Id] INT NOT NULL CONSTRAINT PK_Location PRIMARY KEY IDENTITY(1,1),
	[Name] varchar(128) not null,
	[Value] nvarchar(max) CONSTRAINT Location_Value_Json check( ISJSON([Value]) > 0),
	[Created] datetimeoffset NOT NULL DEFAULT(sysdatetimeoffset()),
	[Updated] datetimeoffset NOT NULL DEFAULT(sysdatetimeoffset())
)
