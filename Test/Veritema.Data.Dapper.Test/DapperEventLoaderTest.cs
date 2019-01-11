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
    public class DapperEventLoaderTest
    {
        private static IDatabaseFactory databaseFactory;
        private static string DataStore = "veritema";
        private static Dictionary<Type, string> EntityMapper;
        private static Dictionary<DayOfWeek, Initializer> InitializerFactory;
        private List<EventRecord> Expected;

        public TestContext TestContext { get; set; }

        private class Initializer
        {
            public string Details { get; set; }
            public MartialArtStyle? Style { get; set; } = new MartialArtStyle?();

            public EventRecord On(DateTime date)
            {
                var record = new EventRecord();
                record.Id = -1;
                record.Title = "Hapkido Track";
                record.Details = Details;
                record.Start = new DateTimeOffset(date.Year, date.Month, date.Day, 18, 30, 0, DateTimeOffset.Now.Offset);
                record.End = record.Start.AddHours(1);
                record.StyleId = Style.HasValue ? (int)Style.Value : new int?();
                record.LocationId = 1;
                record.TypeId = 'C';
                return record;
            }
        }

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
                [typeof(EventRecord)] = "v.Event"
            };

            InitializerFactory = new Dictionary<DayOfWeek, Initializer>
            {
                [DayOfWeek.Monday] = new Initializer { Details = "Traditional Hapkido", Style = MartialArtStyle.Hapkido },
                [DayOfWeek.Tuesday] = new Initializer { Details = "No GI JiuJitsu", Style = MartialArtStyle.JiuJitsu },
                [DayOfWeek.Wednesday] = new Initializer { Details = "Yudo", Style = MartialArtStyle.Judo },
                [DayOfWeek.Thursday] = new Initializer { Details = "Traditional Hapkido", Style = MartialArtStyle.Hapkido },
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

            var offset = DateTime.Now.AddMonths(-1);
            DateTime start = new DateTime(offset.Year, offset.Month, 1);

            using (var connection = new SqlConnection(ConnectionString))
            {
                Expected = Enumerable.Range(0, 180)
                      .Select(i => start.AddDays(i))
                      .Where(i => i.DayOfWeek >= DayOfWeek.Monday && i.DayOfWeek < DayOfWeek.Friday)
                      .Select(i => InitializerFactory[i.DayOfWeek].On(i))
                      .ToList();

                Expected.ForEach(i => connection.Insert(i));
            }
        }

        [TestCleanup]
        public async void Teardown() => await databaseFactory.Destroy(ConnectionString);

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRetrievingEventsWithNoDateRange()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            DapperEventLoader loader = new DapperEventLoader(resolver, new DapperLocationLoader(resolver));
            var events = await loader.GetAsync();
            AreEquivalent(events, Expected);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenLimitingToTheNext30Days()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            DapperEventLoader loader = new DapperEventLoader(resolver, new DapperLocationLoader(resolver));
            DateTime endTime = DateTime.UtcNow.AddDays(31).AddMinutes(-1);
            var events = await loader.GetAsync(to: endTime);

            AreEquivalent(events, Expected.Where(i => i.Start < endTime).ToArray());
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenOmitingThePrevious30Days()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            DapperEventLoader loader = new DapperEventLoader(resolver, new DapperLocationLoader(resolver));
            var events = await loader.GetAsync(from: DateTime.Today);

            AreEquivalent(events, Expected.Where(i => i.Start > DateTime.Today).ToArray());
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRequestingTheCurrentMonth()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            DapperEventLoader loader = new DapperEventLoader(resolver, new DapperLocationLoader(resolver));
            DateTime start = new DateTime(DateTime.UtcNow.Year, DateTime.UtcNow.Month, 1, 0,0,0, DateTimeKind.Utc);
            DateTime end = new DateTime(DateTime.UtcNow.Year, DateTime.UtcNow.Month, DateTime.DaysInMonth(DateTime.UtcNow.Year, DateTime.UtcNow.Month),09,0,0,DateTimeKind.Utc);

            var events = await loader.GetAsync(from: start, to: end);

            AreEquivalent(events, Expected.Where(i => i.Start >= start && i.End <= end).ToArray());
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenRequestingById()
        {
            var resolver = new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });
            DapperEventLoader loader = new DapperEventLoader(resolver, new DapperLocationLoader(resolver));

            var expected = Expected.Skip(5).First();

            var actual = await loader.GetAsync(expected.Id);

            AreEquivalent(new[] { actual }, new[] { expected });
        }


        private static void AreEquivalent(IEnumerable<Event> actual, IEnumerable<EventRecord> expected)
        {
            actual.Count().Should().Be(expected.Count());

            foreach (var a in actual)
            {
                var e = expected.Single(i => i.Id == a.Id);
                a.Description.Should().Be(e.Details);
                a.Title.Should().Be(e.Title);
                a.Style.Should().Be((MartialArtStyle)e.StyleId.Value);
                a.StartUtc.Should().Be(e.Start.UtcDateTime);
                a.EndUtc.Should().Be(e.End.UtcDateTime);
                a.Location.Should().NotBeNull();
                a.Location.Id.Should().Be(e.LocationId.Value);
                a.Location.City.Should().Be("Raleigh");
                a.Location.Name.Should().Be("RIMA Central");
                a.Location.State.Should().Be("NC");
                a.Location.Street.Should().Be("601 East Six Forks Road");
                a.Location.Street2.Should().Be("Suite 100");
                a.Location.Zip.Should().Be("27607");
                a.Location.Contacts.Count().Should().Be(3);
            }
        }

    }
}
