namespace Veritema.Data.Dapper.Test
{
    /// <summary>
    /// A representation of a ConfigurationSection in a standard .NET configuration file
    /// </summary>
    public class ConfigurationSectionInfo
    {
        /// <summary>
        /// Initializes a new instance of the <see cref="ConfigurationSectionInfo" /> class.
        /// </summary>
        public ConfigurationSectionInfo()
        {
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="ConfigurationSectionInfo" /> class
        /// </summary>
        /// <param name="section">The section.</param>
        public ConfigurationSectionInfo(System.Configuration.DefaultSection section)
        {
            Xml = section.SectionInformation.GetRawXml();
            Type = section.SectionInformation.Type;
        }

        /// <summary>
        /// Gets or sets the CLR type of the section.
        /// </summary>
        /// <value>The type.</value>
        public string Type { get; set; }

        /// <summary>
        /// Gets or sets the raw XML describing the section.
        /// </summary>
        /// <value>The XML.</value>
        public string Xml { get; set; }
    }
}
