CREATE TABLE [v].[EventTicket]
(
	[Id] INT NOT NULL CONSTRAINT PK_EventTicket PRIMARY KEY IDENTITY(1,1),
	[Confirmation] varchar(40) NOT NULL,
	[PersonId] int NOT NULL,
	[EventId] INT NOT NULL,
	[Paid] bit NOT NULL default(0),
	[Created] datetimeoffset NOT NULL,
	[Updated] datetimeoffset NOT NULL,
)
