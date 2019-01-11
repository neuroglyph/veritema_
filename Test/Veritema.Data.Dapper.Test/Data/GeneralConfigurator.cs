using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Configuration;
using System.Linq;

namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// Provides a fluent based way to manipulate general configuration information
    /// </summary>
    /// <remarks>This class must be used in an transient fashion.  Failure to do so will result in 
    /// an <see cref="System.Configuration.ConfigurationException"/> exception being thrown</remarks>
    public class GeneralConfigurator : IChangeTracking
    {
        private System.Configuration.Configuration _configuration;
        private readonly Dictionary<string, ConfigurationSectionInfo> _sections = new Dictionary<string, ConfigurationSectionInfo>();

        /// <summary>
        /// Gets the object's changed status.
        /// </summary>
        /// <value><c>true</c> if [is changed]; otherwise, <c>false</c>.</value>
        /// <returns>true if the object’s content has changed since the last call to <see cref="M:System.ComponentModel.IChangeTracking.AcceptChanges"/>; otherwise, false.</returns>
        /// <remarks></remarks>
        public bool IsChanged { get; private set; }

        /// <summary>
        /// Initializes a new instance of the <see cref="T:System.Object"/> class.
        /// </summary>
        /// <remarks></remarks>
        public GeneralConfigurator()
        {
            Load();
        }

        /// <summary>
        /// Resets the object’s state to unchanged by accepting the modifications.
        /// </summary>
        /// <remarks></remarks>
        void IChangeTracking.AcceptChanges()
        {
            _configuration.Commit();
            Load();
            IsChanged = false;
        }

        /// <summary>
        /// Resets the object’s state to unchanged by accepting the modifications.
        /// </summary>
        public GeneralConfigurator AcceptChanges()
        {
            ((IChangeTracking)this).AcceptChanges();
            return this;
        }

        /// <summary>
        /// Bootstrap the Configurator instance.
        /// </summary>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/></returns>
        public GeneralConfigurator Load()
        {
            _configuration = ConfigurationManager.OpenExeConfiguration(ConfigurationUserLevel.None);
            return this;
        }

        /// <summary>
        /// Marks the instance as dirty.
        /// </summary>
        private void MarkDirty() => IsChanged = true;

        /// <summary>
        /// Pops a section that was previously push into the current configuration.
        /// </summary>
        /// <param name="name">The name of the section to restore.</param>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/>.</returns>
        public GeneralConfigurator PopSection(string name)
        {
            if (string.IsNullOrWhiteSpace(name))
            {
                throw new ArgumentNullException(nameof(name));
            }

            if (_sections.ContainsKey(name))
            {
                ConfigurationSection section = _configuration.GetSection(name);
                if (section != null)
                {
                    _configuration.Sections.Remove(name);
                }

                ConfigurationSectionInfo info = _sections[name];
                section = new DefaultSection();
                section.SectionInformation.SetRawXml(info.Xml);
                section.SectionInformation.Type = info.Type;
                _configuration.Sections.Add(name, section);
                MarkDirty();
            }

            return this;
        }

        /// <summary>
        /// Pushes the section on a persisted section stack.
        /// </summary>
        /// <param name="name">The name of the section to save.</param>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/>.</returns>
        /// <remarks>If a section is pushed multiple times only the last instance is persisted.</remarks>
        public GeneralConfigurator PushSection(string name)
        {
            if (string.IsNullOrWhiteSpace(name))
            {
                throw new ArgumentNullException(nameof(name));
            }

            DefaultSection section = _configuration.Sections[name] as DefaultSection;

            if (section != null)
            {
                _sections[name] = new ConfigurationSectionInfo(section);
            }

            return this;
        }

        /// <summary>
        /// Removes a custom section from the configuration.
        /// </summary>
        /// <param name="name">The name of the custom section.</param>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/>.</returns>
        public GeneralConfigurator RemoveCustomSection(string name)
        {
            if (string.IsNullOrWhiteSpace(name))
            {
                throw new ArgumentNullException(nameof(name));
            }

            ConfigurationSection section = _configuration.Sections[name];
            if (section != null)
            {
                _configuration.Sections.Remove(name);
                MarkDirty();
            }

            return this;
        }

        /// <summary>
        /// Removes the scalar.
        /// </summary>
        /// <param name="key">The key.</param>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/></returns>
        /// <remarks>This operation is idempotent.</remarks>
        public GeneralConfigurator RemoveScalar(string key)
        {
            if (string.IsNullOrWhiteSpace(key))
            {
                throw new ArgumentNullException(nameof(key));
            }

            if (_configuration.AppSettings.Settings.AllKeys.Any(i => string.Equals(i, key)))
            {
                _configuration.AppSettings.Settings.Remove(key);
                MarkDirty();
            }

            return this;
        }

        /// <summary>
        /// Define or update the value of a connection string.
        /// </summary>
        /// <param name="name">The key identifying the connection string.</param>
        /// <param name="connectionString">The value of the connection string.</param>
        /// <param name="providerName">Name of the provider.</param>
        /// <returns>An instance of the GeneratorConfigurator</returns>
        public GeneralConfigurator SetConnectionString(string name, string connectionString, string providerName = "System.Data.SqlClient")
        {
            if (string.IsNullOrWhiteSpace(name))
            {
                throw new ArgumentNullException(nameof(name));
            }
            if (string.IsNullOrWhiteSpace(connectionString))
            {
                throw new ArgumentNullException(nameof(connectionString));
            }

            if (_configuration.ConnectionStrings.ConnectionStrings[name] == null)
            {
                _configuration.ConnectionStrings.ConnectionStrings.Add(new ConnectionStringSettings(name, connectionString, providerName));
            }
            else
            {
                _configuration.ConnectionStrings.ConnectionStrings[name].ConnectionString = connectionString;
            }
            MarkDirty();

            return this;
        }

        /// <summary>
        /// Sets the scalar configuration value.
        /// </summary>
        /// <param name="key">The key.</param>
        /// <param name="value">The value.</param>
        /// <returns>An instance of the <see cref="GeneralConfigurator"/></returns>
        public GeneralConfigurator SetScalar(string key, string value)
        {

            if (string.IsNullOrWhiteSpace(key))
            {
                throw new ArgumentNullException(nameof(key));
            }
            if (string.IsNullOrWhiteSpace(value))
            {
                throw new ArgumentNullException(nameof(value));
            }

            if (_configuration.AppSettings.Settings.AllKeys.Any(i => string.Equals(key, i)))
            {
                _configuration.AppSettings.Settings.Remove(key);
            }
            _configuration.AppSettings.Settings.Add(key, value);
            MarkDirty();
            return this;
        }
    }
}
