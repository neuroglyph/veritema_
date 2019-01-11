using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// Defines an entity which is capable of constructing and deconstructing a temporary SQL backend for use in testing.
    /// </summary>
    public interface IDatabaseFactory
    {
        /// <summary>
        /// Gets or sets the name of the application to associate with the SQL connection
        /// </summary>
        /// <value>The name of the application.</value>
        string ApplicationName { get; set; }

        /// <summary>
        /// Gets or sets the name of the directory where the mdf file is deployed.
        /// </summary>
        /// <value>The deployment convention.</value>
        string DeploymentDirectory { get; set; }

        /// <summary>
        /// Gets or sets an association of friendly name to the physical file representing the database instance to utilize.
        /// </summary>
        /// <value>The template names.</value>
        Dictionary<string, string> TargetNames { get; set; }

        /// <summary>
        /// Gets or sets an association of friendly name to the physical file representing the database template.
        /// </summary>
        /// <value>The template names.</value>
        Dictionary<string, string> TemplateNames { get; set; }

        /// <summary>
        /// Builds the connection string for the specified store.
        /// </summary>
        /// <param name="store">The name of the logic data store to clone.</param>
        /// <param name="instance">The identifier for the sql instance.</param>
        /// <returns>the connection string.</returns>
        string Build(string store, Guid instance);

        /// <summary>
        /// Destroy the database instance identified by <paramref name="connectionString" />.
        /// </summary>
        /// <param name="connectionString">The connection string to the database instance to destroy.</param>
        Task Destroy(string connectionString);
    }
}
