using System;
using System.Threading.Tasks;
using FluentAssertions;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using NSubstitute;
using Veritema.Test;

namespace Veritema.Data.Dapper.Test
{
    [TestClass]
    public class DapperEventWriterTest : VeritemaDatabaseSpecification
    {
        [ClassInitialize]
        public static void FixtureSetup(TestContext testContext) => FixtureSetup();

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsCreatedWithoutOptionalValues()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Type = EventType.Class
            };
            var updated = await writer.CreateAsync(expected);
            AssertEquality(expected, updated);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsCreatedWithTheStyleSpecified()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Style = MartialArtStyle.Hapkido,
                Type = EventType.Class
            };
            var updated = await writer.CreateAsync(expected);
            AssertEquality(expected, updated);
        }


        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsCreatedAsConfirmed()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Type = EventType.Class,
                Confirmed = true,
            };
            var updated = await writer.CreateAsync(expected);
            AssertEquality(expected, updated);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsCreatedWithALocation()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Type = EventType.Class,
                Location = new Location { Id = 1 }
            };
            var updated = await writer.CreateAsync(expected);
            AssertEquality(expected, updated);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsApproved()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Type = EventType.Class,
                Location = new Location { Id = 1 }
            };
            var updated = await writer.CreateAsync(expected);
            
            updated.Confirmed = true;
            expected.Confirmed = true;
            updated = await writer.UpdateAsync(updated);

            AssertEquality(expected, updated);
        }


        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenAnEventIsUpdated()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(31),
                Type = EventType.Class,
                Location = new Location { Id = 1 }
            };
            var updated = await writer.CreateAsync(expected);

            expected.Title = updated.Title = "My New Title";
            expected.Description = updated.Description = "My New description";
            expected.StartUtc = updated.StartUtc = DateTime.UtcNow.AddMinutes(60);
            expected.EndUtc = updated.EndUtc = DateTime.UtcNow.AddMinutes(90);
            expected.Location = updated.Location = new Location { Id = 2 };
            expected.Style = updated.Style = MartialArtStyle.TaiChi;
            
            updated = await writer.UpdateAsync(updated);

            AssertEquality(expected, updated);
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenTheEventStartsInThePast()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(-60),
                EndUtc = DateTime.UtcNow.AddMinutes(-30),
                Type = EventType.Class,
            };
            try
            {
                await writer.CreateAsync(expected);
                Assert.Fail();
            }
            catch (ScheduleException ex)
            {
                ex.Message.Should().Be("The event must start in the future.");
            }
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenTheEventEndsBeforeItStarts()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(-30),
                Type = EventType.Class,
            };
            try
            {
                await writer.CreateAsync(expected);
                Assert.Fail();
            }
            catch (ScheduleException ex)
            {
                ex.Message.Should().Be("The event must end after it starts.");
            }
        }

        [TestMethod]
        [TestCategory(Categories.Sql)]
        public async Task WhenTheEventIsTooShort()
        {
            var writer = new DapperEventWriter(InitializesResolver(), Substitute.For<ILocationReader>());
            Event expected = new Event
            {
                Title = "Hapkido Track",
                Description = "Traditional Hapkido grabs, counters, throws and attacks",
                StartUtc = DateTime.UtcNow.AddMinutes(1),
                EndUtc = DateTime.UtcNow.AddMinutes(16),
                Type = EventType.Class,
            };
            try
            {
                await writer.CreateAsync(expected);
                Assert.Fail();
            }
            catch (ScheduleException ex)
            {
                ex.Message.Should().Be("The event must be at least 30 minutes in duration.");
            }
        }

        private void AssertEquality(Event expected, Event actual )
        {
            actual.Id.Should().NotBe(expected.Id);
            actual.Title.Should().Be(expected.Title);
            actual.Description.Should().Be(expected.Description);
            actual.StartUtc.Should().Be(expected.StartUtc);
            actual.EndUtc.Should().Be(expected.EndUtc);
            actual.Style.HasValue.Should().Be(expected.Style.HasValue);
            if (expected.Style.HasValue)
            {
                actual.Style.Value.Should().Be(expected.Style.Value);
            }
            actual.Location?.Id.Should().Be(expected.Location?.Id);
            actual.Confirmed.Should().Be(expected.Confirmed);
            (DateTimeOffset.Now - actual.Updated).Should().BeLessThan(new TimeSpan(0, 0, 5));
            actual.Type.Should().Be(actual.Type);
        }
    }
}
