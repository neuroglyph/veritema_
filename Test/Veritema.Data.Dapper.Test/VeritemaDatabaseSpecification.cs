using System;
using System.Collections.Generic;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// Core specification definition to test veritema database logic 
    /// </summary>
    [DeploymentItem("Veritema.mdf", "Database")]
    public abstract class VeritemaDatabaseSpecification
    {
        private static IDatabaseFactory databaseFactory;
        private static string DataStore = "veritema";

        public TestContext TestContext { get; set; }

        /// <summary>
        /// Gets or sets the connection string.
        /// </summary>
        /// <value>The connection string.</value>
        protected string ConnectionString
        {
            get { return (string)TestContext.Properties["cxnstr"]; }
            set { TestContext.Properties["cxnstr"] = value; }
        }


        protected static void FixtureSetup()
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
        }

        /// <summary>
        /// Initializeses a <see cref="IConnectionStringResolver"/> instance.
        /// </summary>
        /// <returns>The populated <see cref="IConnectionStringResolver"/>.</returns>
        protected IConnectionStringResolver InitializesResolver() => new StaticConnectionStringResolver(new Dictionary<string, string> { ["v"] = ConnectionString });

        protected virtual void SeedDatabase()
        {
        }

        [TestInitialize]
        public virtual void Setup()
        {
            ConnectionString = databaseFactory.Build(DataStore, Guid.NewGuid());
            SeedDatabase();
        }

        

        [TestCleanup]
        public virtual async void Teardown() { await databaseFactory.Destroy(ConnectionString); }


        


        
    }
}
