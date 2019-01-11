using System;
using System.Diagnostics;

namespace VeritemaMVC_3.Models
{
    /// <summary>
    /// The client side representation of an Event
    /// </summary>
    [DebuggerDisplay("{DebuggerDisplay}")]
    public class ScheduleEvent
    {
        public const string DateTimeFormat = "MM/dd/yyyy HH:mm";

        private string DebuggerDisplay => $"{id}{(tid.HasValue ? "("+tid.Value.ToString() +")": string.Empty)} from {start_date}->{end_date} at {event_location}";

        /// <summary>
        /// Whether an event can be edited or not (controls which buttons are displayed in lightbox form)
        /// based on user type, schedule event type, etc.
        /// persist this in database?
        /// </summary>
        public bool editable { get; set; }

        /// <summary>
        /// Gets or sets the ending time of the event in the format <see cref="DateTimeFormat"/>.
        /// </summary>
        /// <value>The end date.</value>
        public string end_date { get; set; }
        
        /// <summary>
        /// Gets or sets the location of the event.
        /// </summary>
        /// <value>The event location.</value>
        public string event_location_id { get; set; }

        /// <summary>
        /// Gets the event location.
        /// </summary>
        /// <value>The event location.</value>
        public ScheduleLocation event_location { get; set; }

        /// <summary>
        /// Gets or sets the event identifier.
        /// </summary>
        /// <value>The identifier.</value>
        public Int64 id { get; set; }

        /// <summary>
        /// Gets or sets the starting time of the event in the format <see cref="DateTimeFormat"/>
        /// </summary>
        /// <value>The start date.</value>
        public string start_date { get; set; }

        /// <summary>
        /// Gets or sets the martial arts style that will be the focus of the session.
        /// </summary>
        /// <value>The style.</value>
        public string style { get; set; }

        /// <summary>
        /// Gets or sets the text name of the event.
        /// </summary>
        /// <value>The text.</value>
        public string text { get; set; }

        /// <summary>
        /// Gets or sets the new ID field if it was changed server side.
        /// </summary>
        /// <value>The tid.</value>
        public Int64? tid { get; set; }

        /// <summary>
        /// Gets or sets the title of the event.
        /// </summary>
        /// <value>The title.</value>
        public string title { get; set; }

        /// <summary>
        /// Gets or sets type of the event.
        /// </summary>
        /// <value>
        /// <para>A value from a fixed domain</para>
        /// <list type="bullet">
        /// <item>Class</item>
        /// <item>Private</item>
        /// <item>Seminar</item>
        /// </list>
        /// </value>
        public string type { get; set; }



        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleEvent"/> class.
        /// </summary>
        public ScheduleEvent()
        {
            // 10.27.2016 (RG) for testing
            editable = false;
        }
    }
}
