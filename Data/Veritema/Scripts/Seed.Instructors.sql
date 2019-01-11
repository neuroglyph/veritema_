print 'Executing Script Seed.Instructors.sql'

SET IDENTITY_INSERT [v].[Person] ON

MERGE INTO [v].Person as T
USING
(
	VALUES
	 (1, 'John', 'Taube', 'John Taube is the school''s Founder and Soke of Seido-Ryu Jitsu. 6th Dan (Hapkido - Taekwondo - Jujitsu); 3rd Dan Judo; Kru Muay Thai;  Brown Belt RCJ Machado Brazilian Jiu-Jitsu','/images/JohnTaube.JPG',GetDate()) ,
     (2,'Chris', 'Tricoli', 'Chris Tricoli has owned and operated martial arts schools for over twenty-one years. He has had the rare privilege to represent such awesome lineage such a, Sifu Jeffery Gay, William White, Carlos Machado, Royce Gracie, Scott Francis, Professor Ernie Cates, GM Cyrus, GM Jae Han Ji, GM In Sun Seo, GM West, Dr. Kimm, and Master John Taube', '/images/chris_tricoli.jpg',GetDate()),
	 (3, 'Ben', 'Tice', 'More Info Coming Soon','/images/site/equip-joomla-fitness-theme-logo.png',GetDate()),
	 (4, 'Eric', 'Swanson', 'More Info Coming Soon','/images/site/equip-joomla-fitness-theme-logo.png', GetDate())
)
AS
	S
	(
		Id
		,[First]
		,[Last] 
		,[Description]
		,ImageUrl
		,Created
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id,  [First], [Last], [Description], ImageUrl, Created)
	VALUES (Id, [First], [Last], [Description], ImageUrl, Created)
WHEN MATCHED THEN
UPDATE SET
	T.[First] = S.[First]
	,T.[Last] = S.[Last] 
	,T.[Description] = S.[Description]
	,T.ImageUrl = S.ImageUrl
	,T.Created = S.Created
WHEN NOT MATCHED BY SOURCE THEN
DELETE;

SET IDENTITY_INSERT [v].[Person] OFF

print 'Completed Script Seed.Person.sql'
print '-------------------------------------------------'
