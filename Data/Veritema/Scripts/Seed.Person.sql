print 'Executing Script Seed.Person.sql'

SET IDENTITY_INSERT [v].[Person] ON

MERGE INTO [v].Person as T
USING
(
	VALUES
	(1, '{"First":"Chris","Last":"Tricoli","Description":"I was introduced to the world of martial art philosophy as a young boy by my aunt who is Japanese. After living with my aunt and uncle I moved to Texas where my neighbor studied under the greatest teachers in the area. Later I moved to North Carolina and started showing martial arts to Military Personnel in Cherry Point Marine corps air station. After a few yrs teaching I moved to Greenville NC. and started my first school in 1995 while also learning the Chinese arts such as gung fu and Tai Chi, along with karate and Hapkido, since then I moved to Raleigh which is where I currently call home. I still study and practice what I teach, The relationships that my training has developed on and off the mat is one that cannot be explained in words. Its has been a true honor to have trained under the best in the world, and it’s a greater honor that these teachers are also my friends. I have had a rare privilege to represent such awesome lineage such a, Sifu Jeffery Gay, William White, Carlos Machado, Royce Gracie, Scott Francis, Professor Ernie Cates, GM Cyrus, GM Jae Han Ji, GM In Sun Seo, GM West, Dr. Kimm, and Master John Taube.", "ImageUrl": "./Martial Arts Classes Raleigh_files/chris_tricoli.jpg","Temporary":false, "Verified":true,"Contacts":["sms:9196017534"]}'),
	(2, '{"First":"John", "Last": "Taube", "Description":"John Taube is the school''s Founder and Soke of Seido-Ryu Jitsu. 6th Dan (Hapkido - Taekwondo - Jujitsu); 3rd Dan Judo; Kru Muay Thai; Black Belt RCJ Machado Brazilian Jiu-Jitsu.", "ImageUrl":"./Martial Arts Classes Raleigh_files/John Taube.JPG","Temporary":false,"Verified":true}'),
	(3, '{"First":"Erick","Last":"Swansen", "Verified": true}'),
	(4, '{"First":"Ben", "Last": "Hice"}'),
	(5, '{"First":"Richard","Last":"Gay","Contacts":["mailto:Rich5673@gmail.com","sms:9196706774"]}'),
	(6, '{"First":"Tedford","Last":"Johnson","Contacts":["mailto:veritema@tedfordjohnson.com","sms:9192851296"]}')
)
AS
	S
	(
		Id
		,[Value]
	)
ON
	T.Id = S.Id
WHEN NOT MATCHED BY TARGET THEN
	INSERT (Id,[Value],Created,Updated)
	VALUES (Id,[Value],SYSDATETIMEOFFSET(),SYSDATETIMEOFFSET())
WHEN MATCHED THEN
UPDATE SET
	T.[Value] = S.[Value],
	T.Updated = SYSDATETIMEOFFSET()
;

SET IDENTITY_INSERT [v].[Person] OFF

print 'Completed Script Seed.Person.sql'
print '-------------------------------------------------'

