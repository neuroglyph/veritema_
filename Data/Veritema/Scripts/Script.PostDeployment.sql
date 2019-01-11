/*
Post-Deployment Script Template							
--------------------------------------------------------------------------------------
 This file contains SQL statements that will be appended to the build script.		
 Use SQLCMD syntax to include a file in the post-deployment script.			
 Example:      :r .\myfile.sql								
 Use SQLCMD syntax to reference a variable in the post-deployment script.		
 Example:      :setvar TableName MyTable							
               SELECT * FROM [$(TableName)]					
--------------------------------------------------------------------------------------
*/
SET NOCOUNT ON
:r .\Seed.Location.sql
:r .\Seed.MartialStyle.sql
:r .\Seed.EventType.sql
:r .\Seed.AwsChannel.sql
--:r .\Seed.Instructors.sql
:r .\Seed.EventType.sql
:r .\Seed.Person.sql