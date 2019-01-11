﻿CREATE TABLE [v].[Person]
(
	[Id] INT NOT NULL CONSTRAINT PK_Person PRIMARY KEY IDENTITY(1,1), 
	[Value] nvarchar(max) CONSTRAINT Person_Value_Json CHECK(ISJSON([Value]) > 0),
    [Created] DATETIMEOFFSET NOT NULL DEFAULT(SYSDATETIMEOFFSET()),
    [Updated] DATETIMEOFFSET NOT NULL DEFAULT(SYSDATETIMEOFFSET())
)