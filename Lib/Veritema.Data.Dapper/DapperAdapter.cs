using System;
using System.IO;
using System.Linq;
using System.Reflection;

namespace Veritema.Data
{
    /// <summary>
    /// Provides basic functionality when utilizing dapper
    /// </summary>
    public abstract class DapperAdapter
    {
        private readonly Assembly Asm = Assembly.GetExecutingAssembly();


        /// <summary>
        /// Loads the a script stored as an embedded resource.
        /// </summary>
        /// <param name="name">The name of the script.</param>
        /// <returns>the TSQL contained within the script.</returns>
        /// <exception cref="ResourceNotFoundException">Thrown when the specified script cannot be located</exception>
        protected string LoadScript(string name)
        {
            string path = Asm.GetManifestResourceNames().FirstOrDefault(i => i.IndexOf(name, StringComparison.OrdinalIgnoreCase) > -1);
            if (string.IsNullOrWhiteSpace(path))
            {
                throw new ResourceNotFoundException($"Unable to load the SQL script {name}");
            }

            string sql;
            using (var stream = Asm.GetManifestResourceStream(path))
            using (var tsqlReader = new StreamReader(stream))
            {
                sql = tsqlReader.ReadToEnd();
            }
            return sql;
        }


    }
}
