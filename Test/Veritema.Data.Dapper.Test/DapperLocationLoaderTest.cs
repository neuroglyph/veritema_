using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Threading.Tasks;
using Dapper.Contrib.Extensions;
using FluentAssertions;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using Newtonsoft.Json;
using Veritema.Test;

namespace Veritema.Data.Dapper.Test
{
    [DeploymentItem("Veritema.mdf", "Database")]
    [TestClass]
    public class DapperLocationLoaderTest
    {
        private static IDatabaseFactory databaseFactory;
        private static string DataStore = "veritema";
        private static Dictionary<Type, string> EntityMapper;
        private static IEnumerable<Location> Seeded = new Location[0];
        private static IEnumerable<Location> Injected = new Location[0];

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

        private class location
        {
            [Key]
            public int Id { get; set; }
            public string Name { get; set; }
            public string Value { get; set; }
            public DateTimeOffset Created { get; set; }
            public DateTimeOffset Updated { get; set; }

            public location(Location location)
            {
                Id = location.Id;
                Name = location.Name;
                Value = JsonConvert.SerializeObject(location);
                Updated = DateTimeOffset.Now;
                Created = DateTimeOffset.Now;
            }
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
                [typeof(location)] = "v.Location",
            };

            Seeded = new[] { new Location{
                Id = 1,
                Name = "RIMA Central",
                Street = "601 East Six Forks Road",
                Street2 = "Suite 100",
                City = "Raleigh",
                State = "NC",
                Zip = "27607",
                Contacts =new [] {  new Uri("tel:9196017534"), new Uri("https://veritema.azurewebsites.net"), new Uri("tel:9198284447") }
            },
            new Location {
                Id = 2,
                Name = "Cape Fear Martial Arts",
                Street = "6832 Market St",
                City     = "Wilmington",
                State = "NC",
                Zip = "28405",
                Contacts =new [] { new Uri("tel:9106862678") }
            } };
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

            var offset = DateTime.Now.AddMonths(-1);
            DateTime start = new DateTime(offset.Year, offset.Month, 1);

            using (var connection = new SqlConnection(ConnectionString))
            {
                Injected = new[] {
                    new Location { Contacts = new[] { new Uri("tel:9192397314") }, City = "Raleigh", Name = "Allscripts", State = "NC", Street = "8529 Six Forks Rd.", Street2 = "Forum IV", Zip = "27615" },
                    new Location { City = "Houston", Name = "GC Services", State = "TX", Street = "6330 Gulfton Dr.", Zip = "77081" }
                };
                Injected.Select(i => new { Actual = i, Record = new location(i) }).ToList().ForEach(i =>
                  {
                      connection.Insert(i.Record);
                      i.Actual.Id = i.Record.Id;
                  });
            }
        }

        [TestCleanup]
        public async void Teardown()
        {
            await databaseFactory.Destroy(ConnectionString);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRetrievingAll()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            var loader = new DapperLocationLoader(resolver);
            var actual = await loader.GetAsync();
            actual.Should().NotBeNull();
            actual.Count().Should().Be(Injected.Count() + Seeded.Count());
            AreEquivalent(Seeded.Union(Injected), actual);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRequestingById()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            var loader = new DapperLocationLoader(resolver);

            var actual = await loader.GetAsync(Seeded.First().Id);
            AreEquivalent(new[] { actual }, new[] { Seeded.First() });
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRequestingAnIdThatDoesNotExist()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            var loader = new DapperLocationLoader(resolver);

            var actual = await loader.GetAsync(10000);
            actual.Should().BeNull();
        }

        private static void AreEquivalent(IEnumerable<Location> actual, IEnumerable<Location> expected)
        {
            actual.Count().Should().Be(expected.Count());

            foreach (var a in actual)
            {
                var e = expected.Single(i => i.Id == a.Id);
                a.City.Should().Be(e.City);
                a.Id.Should().Be(e.Id);
                a.Name.Should().Be(e.Name);
                a.State.Should().Be(e.State);
                a.Street.Should().Be(e.Street);
                a.Street2.Should().Be(e.Street2);
                a.Zip.Should().Be(e.Zip);
                a.Contacts.Should().BeEquivalentTo(e.Contacts);
            }
        }
    }
}

