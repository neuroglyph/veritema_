using System.Configuration;
using System.Linq;

namespace Veritema.Notification.Configuration
{
    /// <summary>
    /// A .NET configuration section to hold the details on how to integrate with the SMTP engine.
    /// </summary>
    /// <seealso cref="System.Configuration.ConfigurationSection" />
    public class SmtpConfigurationSection : ConfigurationSection
    {
        private static readonly ConfigurationProperty _useTls = new ConfigurationProperty("useTls", typeof(bool), false, ConfigurationPropertyOptions.None);
        private static readonly ConfigurationProperty _server = new ConfigurationProperty("server", typeof(string), null, ConfigurationPropertyOptions.None);
        private static readonly ConfigurationProperty _port = new ConfigurationProperty("port", typeof(int), 25, ConfigurationPropertyOptions.None);
        private static readonly ConfigurationProperty _agentName = new ConfigurationProperty("agentName", typeof(string), "Veritema");
        private static readonly ConfigurationProperty _agentAddress = new ConfigurationProperty("agentAddress", typeof(string), "SaasVerifier.IHE@allscripts.com");
        private static readonly ConfigurationProperty _user = new ConfigurationProperty("user", typeof(string));
        private static readonly ConfigurationProperty _password = new ConfigurationProperty("password", typeof(string));

        private static readonly ConfigurationPropertyCollection _properties = new ConfigurationPropertyCollection
        {
            _agentName, _agentAddress, _server,_port,_useTls, _user, _password
        };

        /// <summary>
        /// Gets or sets the display name of the agent which is sending the email.
        /// </summary>
        /// <value>The name of the agent.</value>
        public string AgentName
        {
            get { return (string)this[_agentName]; }
            set { this[_agentName] = value; }
        }

        /// <summary>
        /// Gets or sets the RFC5322 mail address for the agent
        /// </summary>
        /// <value>The agent address.</value>
        public string AgentAddress
        {
            get { return (string)this[_agentAddress]; }
            set { this[_agentAddress] = value; }
        }

        /// <summary>
        /// Gets a value indicating whether TLS should be enabled when communicating with the server.
        /// </summary>
        /// <value><c>true</c> if TLS will be utilized; otherwise, <c>false</c>.</value>
        public bool EnableTls
        {
            get { return (bool)this[_useTls]; }
            set { this[_useTls] = value; }
        }

        /// <summary>
        /// Gets the collection of properties.
        /// </summary>
        /// <value>The properties.</value>
        protected override ConfigurationPropertyCollection Properties
        {
            get
            {
                var collection = new ConfigurationPropertyCollection();
                base.Properties.Cast<ConfigurationProperty>()
                               .Union(_properties.Cast<ConfigurationProperty>())
                               .ToList()
                               .ForEach(i => collection.Add(i));

                return collection;
            }
        }

        /// <summary>
        /// Gets the password to use when authnenticating against the server.
        /// </summary>
        /// <value>The password.</value>
        public string Password
        {
            get { return (string)this[_password]; }
            set { this[_password] = value; }
        }

        /// <summary>
        /// Gets or sets the port to which the SMTP server is bound.
        /// </summary>
        /// <value>The port.</value>
        public int Port
        {
            get { return (int)this[_port]; }
            set { this[_port] = value; }
        }

        /// <summary>
        /// Gets or sets DNS name or IP address of the SMTP relay server.
        /// </summary>
        /// <value>The server.</value>
        public string Server
        {
            get { return (string)this[_server]; }
            set { this[_server] = value; }
        }

        /// <summary>
        /// Gets the user name to use when authenticating against the server.
        /// </summary>
        /// <value>The user.</value>
        public string User
        {
            get { return (string)this[_user]; }
            set { this[_user] = value; }
        }
    }
}
