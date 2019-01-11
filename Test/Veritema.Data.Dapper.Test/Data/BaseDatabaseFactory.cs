using System;
using System.Collections.Generic;
using System.Configuration;
using System.Threading.Tasks;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// The basic implementation of <see cref="IDatabaseFactory"/> to provide some infrastructure.
    /// </summary>
    public abstract class BaseDatabaseFactory : IDatabaseFactory
    {
        /// <summary>
        /// Gets or sets the name of the application to associate with the SQL connection
        /// </summary>
        /// <value>The name of the application.</value>
        public virtual string ApplicationName { get; set; } = "MSTest";

        /// <summary>
        /// Gets or sets the name of the directory where the mdf file is deployed.
        /// </summary>
        /// <value>The deployment convention.</value>
        public virtual string DeploymentDirectory { get; set; } = "Database";

        /// <summary>
        /// Gets or sets an association of friendly name to the physical file representing the database instance to utilize.
        /// </summary>
        /// <value>The template names.</value>
        public Dictionary<string, string> TargetNames { get; set; } = new Dictionary<string, string>();

        /// <summary>
        /// Gets or sets an association of friendly name to the physical file representing the database template.
        /// </summary>
        /// <value>The template names.</value>
        public Dictionary<string, string> TemplateNames { get; set; } = new Dictionary<string, string>();

        /// <summary>
        /// Builds the connection string for the specified store.
        /// </summary>
        /// <param name="store">The name of the logic data store to clone.</param>
        /// <param name="instance">The identifier for the sql instance.</param>
        /// <returns>the connection string.</returns>
        public abstract string Build(string store, Guid instance);

        /// <summary>
        /// Destroy the catalog located at the provided <paramref name="connectionString" />.
        /// </summary>
        /// <param name="connectionString">The connection string.</param>
        public virtual async Task Destroy(string connectionString) => await Task.Yield();

        /// <summary>
        /// Determines the source.
        /// </summary>
        /// <param name="store">The store.</param>
        /// <returns>System.String.</returns>
        /// <exception cref="System.Configuration.ConfigurationErrorsException"></exception>
        protected string DetermineSource(string store)
        {
            string filename;

            if (!TemplateNames.TryGetValue(store, out filename))
            {
                throw new ConfigurationErrorsException($"The requested data store {store} is unknown.");
            }

            return filename;
        }

        /// <summary>
        /// Determines the target.
        /// </summary>
        /// <param name="store">The store.</param>
        /// <param name="instance">The instance.</param>
        /// <returns>System.String.</returns>
        /// <exception cref="System.Configuration.ConfigurationErrorsException"></exception>
        protected string DetermineTarget(string store, Guid instance)
        {
            string targetName;
            if (!TargetNames.TryGetValue(store, out targetName))
            {
                throw new ConfigurationErrorsException($"The target name for data store {store} is unknown.");
            }
            targetName = string.Format(targetName, instance);
            return targetName;
        }
    }
}
