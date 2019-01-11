using System.Xml.Serialization;

namespace VeritemaMVC_3.Models
{
    [XmlRoot("complete")]
    public class ScheduleLocationComboResponse
    {
        [XmlElement("option")]
        public ScheduleLocationCombo[] Locations { get; set; }
    }
}