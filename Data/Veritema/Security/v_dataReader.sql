CREATE ROLE [v_dataReader] AUTHORIZATION [dbo]
GO

GRANT SELECT on SCHEMA::[v] to [v_dataReader]
GO