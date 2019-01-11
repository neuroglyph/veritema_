using System;
using System.Data.SqlClient;
using System.Diagnostics;
using System.IO;
using System.Reflection;
using System.Threading.Tasks;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// An implementation of <see cref="IDatabaseFactory"/> which utilizes MSSQLLocalDB.
    /// </summary>
    /// <remarks>This address the issue found http://stackoverflow.com/questions/26346647/the-database-cannot-be-opened-because-it-is-version-782-this-server-supports-ve</remarks>
    public class MSSqlLocalDBDatabaseFactory : BaseDatabaseFactory
    {
        private readonly Stopwatch stopwatch = new Stopwatch();

        /// <summary>
        /// Builds the connection string for the specified store.
        /// </summary>
        /// <param name="store">The name of the logic data store to clone.</param>
        /// <param name="instance">The identifier for the sql instance.</param>
        /// <returns>the connection string.</returns>
        public override string Build(string store, Guid instance)
        {
            string executionLocation = Path.GetDirectoryName(Assembly.GetExecutingAssembly().CodeBase);

            string filename = DetermineSource(store);
            string targetName = DetermineTarget(store, instance);

            Uri source = new Uri(Path.Combine(executionLocation, DeploymentDirectory, filename));
            Uri target = new Uri(Path.Combine(executionLocation, DeploymentDirectory, targetName));

            stopwatch.Restart();
            File.Copy(source.LocalPath, target.LocalPath);
            stopwatch.Stop();

            Trace.WriteLine($"The data story {store} was deployed in {stopwatch.Elapsed}");

            SqlConnectionStringBuilder builder = new SqlConnectionStringBuilder();
            builder.ApplicationName = ApplicationName;
            builder.DataSource = @"(LocalDb)\MSSQLLocalDB";
            // localdb can only have a single catalog with a given name so entropy must be added
            builder.InitialCatalog = $"{store}_{instance}";
            builder.IntegratedSecurity = true;
            builder.MultipleActiveResultSets = true;
            builder.Pooling = false;
            builder.AttachDBFilename = target.LocalPath;
            builder.Enlist = false;

            return builder.ToString();
        }

        /// <summary>
        /// Destroy the database instance identified by <paramref name="connectionString" />.
        /// </summary>
        /// <param name="connectionString">The connection string to the database instance to destroy.</param>
        public override async Task Destroy(string connectionString)
        {
            Trace.WriteLine($"Dropping Catalog: {connectionString}");

            SqlConnectionStringBuilder original = new SqlConnectionStringBuilder(connectionString);

            stopwatch.Restart();
            try
            {
                SqlConnectionStringBuilder builder = new SqlConnectionStringBuilder
                {
                    InitialCatalog = "master",
                    DataSource = original.DataSource,
                    IntegratedSecurity = original.IntegratedSecurity,
                    MultipleActiveResultSets = original.MultipleActiveResultSets,
                    ApplicationName = original.ApplicationName,
                };

                string sql = $"sp_detach_db [{original.InitialCatalog}], true";
                Trace.WriteLine($"Executing {sql} on {builder}");
                using (SqlConnection cxn = new SqlConnection(builder.ToString()))
                using (SqlCommand cmd = new SqlCommand(sql, cxn))
                {
                    await cxn.OpenAsync();
                    int result = await cmd.ExecuteNonQueryAsync();
                    if (result != -1)
                    {
                        Trace.WriteLine($"Detach of {original.InitialCatalog} failed");
                    }
                    cxn.Close();
                }

            }
            catch (Exception ex)
            {
                Trace.WriteLine($"Unable to destroy the test database {original.InitialCatalog}");
                Trace.WriteLine(ex);
            }
            stopwatch.Stop();
            Trace.WriteLine($"Drop completed in {stopwatch.Elapsed}.");
            await Task.Yield();
        }
    }
}
