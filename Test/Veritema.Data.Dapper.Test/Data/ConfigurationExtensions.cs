using System;
using System.Configuration;
using System.IO;
using System.Reflection;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// A set of functional extensions to <see cref="Configuration"/>
    /// </summary>
    public static class ConfigurationExtensions
    {

        /// <summary>
        /// Commits the changes to disk and force a reload.
        /// </summary>
        /// <param name="config">The configuration.</param>
        /// <remarks>
        /// This method is very framework dependant.  The current implementation is only known to work in .NET 4.0
        /// </remarks>
        public static void Commit(this System.Configuration.Configuration config) => Commit(config, null);

        /// <summary>
        /// Commits the changes to disk and force a reload.
        /// </summary>
        /// <param name="config">The configuration.</param>
        /// <param name="filePath">The file path where the configuration should be saved.</param>
        /// <remarks>
        /// This method is very framework dependant.  The current implementation is only known to work in .NET 4.0
        /// </remarks>
        public static void Commit(this System.Configuration.Configuration config, string filePath)
        {
            if (config == null)
            {
                throw new ArgumentNullException(nameof(config));
            }


            if (string.IsNullOrWhiteSpace(filePath) || string.Equals(Path.GetFileName(config.FilePath), Path.GetFileName(filePath)))
            {
                config.Save(ConfigurationSaveMode.Minimal);
            }
            else
            {
                config.SaveAs(filePath, ConfigurationSaveMode.Minimal);
            }

            Type t = typeof(ConfigurationManager);
            BindingFlags flags = BindingFlags.NonPublic | BindingFlags.Static;

            // clear the configuration state
            FieldInfo field;

            field = t.GetField("s_initState", flags);
            if (field == null)
            {
                throw new InvalidOperationException($"The field s_initState is no longer present.");
            }
            field.SetValue(null, 0);

            field = t.GetField("s_configSystem", flags);
            if (field == null)
            {
                throw new InvalidOperationException("The field s_configSystem is no longer present.");
            }
            field.SetValue(null, null);

            // force a reload
            MethodInfo method = t.GetMethod("PrepareConfigSystem", flags);
            if (method == null)
            {
                throw new InvalidOperationException("The method PrepareConfigSystem is no longer present.");
            }
            method.Invoke(null, null);
        }
    }
}
