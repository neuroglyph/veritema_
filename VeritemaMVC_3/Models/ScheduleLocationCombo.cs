using System.Xml.Serialization;

namespace VeritemaMVC_3.Models
{
    [XmlRoot("option")]
    public class ScheduleLocationCombo
    {
        [XmlAttribute("value")]
        public string Id { get; set; }

        [XmlText]
        public string Label { get; set; }
    }

}