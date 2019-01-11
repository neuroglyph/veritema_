CREATE ROLE [v_dataWriter] AUTHORIZATION [dbo]
GO

GRANT UPDATE on SCHEMA::[v] to [v_dataWriter]
GO

GRANT DELETE on SCHEMA::[v] to [v_dataWriter]
GO

GRANT INSERT on SCHEMA::[v] to [v_dataWriter]
GO

GRANT EXECUTE on SCHEMA::[v] to [v_dataWriter]
GO
