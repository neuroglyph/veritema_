CREATE TABLE [v].[AwsChannel]
(
	[Id] INT NOT NULL PRIMARY KEY, 
    [Name] VARCHAR(50) NULL, 
    [ARN] VARCHAR(100) NOT NULL, 
    [ChannelType] CHAR(1) NOT NULL  /* T = Topic, S = Subscription */, 
    [ParentId] INT NULL,			/* Subscriptions will have topics as parent entities */
    [Protocol] VARCHAR(10) NULL,	/* sms, email, http */
	[EndPoint] varchar(20) NULL		/* This is the email address, the phone number... */
)
