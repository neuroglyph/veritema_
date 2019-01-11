using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Threading.Tasks;
using Dapper.Contrib.Extensions;
using FluentAssertions;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Veritema.Test;

namespace Veritema.Data.Dapper.Test
{
    [DeploymentItem("Veritema.mdf", "Database")]
    [TestClass]
    public class DapperPersonLoaderTest
    {
        private static IDatabaseFactory databaseFactory;
        private static string DataStore = "veritema";
        private static Dictionary<Type, string> EntityMapper;
        private static IEnumerable<Person> Seeded = new Person[0];
        private static IEnumerable<Person> Injected = new Person[0];

        public TestContext TestContext { get; set; }

        /// <summary>
        /// Gets or sets the connection string.
        /// </summary>
        /// <value>The connection string.</value>
        private string ConnectionString
        {
            get { return (string)TestContext.Properties["cxnstr"]; }
            set { TestContext.Properties["cxnstr"] = value; }
        }

        [ClassInitialize]
        public static void FixtureSetup(TestContext testContext)
        {
            databaseFactory = new MSSqlLocalDBDatabaseFactory
            {
                TemplateNames = new Dictionary<string, string>
                {
                    [DataStore] = "veritema.mdf"
                },
                TargetNames = new Dictionary<string, string>
                {
                    [DataStore] = "Veritema_{0}.mdf"
                }
            };

            EntityMapper = new Dictionary<Type, string>
            {
                [typeof(Person)] = "v.Person",
            };

            Seeded = new[] {
                new Person {Id = 1, First = "Chris", Last = "Tricoli", Description = "I was introduced to the world", ImageUrl = new Uri("./Martial Arts Classes Raleigh_files/chris_tricoli.jpg", UriKind.RelativeOrAbsolute), Temporary = false, Verified = true, Contacts = new [] {new Uri("sms:9196017534") } },
                new Person {Id = 2, First= "John", Last = "Taube", Description = "John Taube is the school's Founder and Soke of Seido-Ryu Jitsu",ImageUrl = new Uri("./Martial Arts Classes Raleigh_files/John Taube.JPG",UriKind.RelativeOrAbsolute), Temporary = false, Verified = true,  },
                new Person {Id = 3, First = "Erick", Last = "Swansen", Verified = true },
                new Person {Id = 4, First = "Ben", Last = "Hice" },
                new Person {Id = 5, First = "Richard", Last = "Gay", Contacts=new [] {new Uri("mailto:Rich5673@gmail.com") }},
                new Person {Id = 6, First = "Tedford", Last="Johnson", Contacts=new [] {new Uri("mailto:veritema@tedfordjohnson.com"),new Uri("sms:9192851296") } }
            };
        }

        [TestInitialize]
        public void Setup()
        {
            ConnectionString = databaseFactory.Build(DataStore, Guid.NewGuid());
            SeedDatabase();
        }

        private void SeedDatabase()
        {
            SqlMapperExtensions.TableNameMapper = type => EntityMapper[type];

            using (var connection = new SqlConnection(ConnectionString))
            {
                // TODO inject in some test data
                //Injected = new[] {
                //    new Location { Contacts = new[] { new Uri("tel:9192397314") }, City = "Raleigh", Name = "Allscripts", State = "NC", Street = "8529 Six Forks Rd.", Street2 = "Forum IV", Zip = "27615" },
                //    new Location { City = "Houston", Name = "GC Services", State = "TX", Street = "6330 Gulfton Dr.", Zip = "77081" }
                //};
                //Injected.Select(i => new { Actual = i, Record = new location(i) }).ToList().ForEach(i =>
                //{
                //    connection.Insert(i.Record);
                //    i.Actual.Id = i.Record.Id;
                //});
            }
        }

        [TestCleanup]
        public async void Teardown()
        {
            await databaseFactory.Destroy(ConnectionString);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRetrievingAllPeople()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            var loader = new DapperPersonLoader(resolver);
            var actual = await loader.GetAsync();
            actual.Should().NotBeNull();
            actual.Count().Should().Be(Injected.Count() + Seeded.Count());
            AreEquivalent(Seeded.Union(Injected), actual);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRetrievingAPersonById()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            var loader = new DapperPersonLoader(resolver);
            var expected = Seeded.First();
            var actual = await loader.GetAsync(expected.Id);
            
            AreEquivalent(expected, actual);
        }

        private static void AreEquivalent(IEnumerable<Person> actual, IEnumerable<Person> expected)
        {
            actual.Count().Should().Be(expected.Count());

            foreach (var a in actual)
            {
                var e = expected.Single(i => i.Id == a.Id);
                AreEquivalent(a, e);
            }
        }

        private static void AreEquivalent(Person a, Person e)
        {
            if (e.Description == null)
            {
                a.Description.Should().BeNull();
            }
            else
            {
                e.Description.Should().StartWith(a.Description);
            }
            a.First.Should().Be(e.First);
            a.ImageUrl.Should().Be(e.ImageUrl);
            a.Last.Should().Be(e.Last);
            a.Temporary.Should().Be(e.Temporary);
            e.Updated.Should().BeAfter(new DateTimeOffset(2016, 11, 1, 0, 0, 0, new TimeSpan()));
            a.Verified.Should().Be(e.Verified);
            a.Contacts.Should().BeEquivalentTo(e.Contacts);
        }
    }
}
