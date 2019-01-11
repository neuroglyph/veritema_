using System;

namespace Veritema.Data
{

    /// <summary>
    /// Represents something that went awry around scheduling
    /// </summary>
    /// <seealso cref="System.Exception" />
    [Serializable]
    public class ScheduleException : Exception
    {
        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleException"/> class.
        /// </summary>
        public ScheduleException() { }

        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleException"/> class.
        /// </summary>
        /// <param name="message">The message that describes the error.</param>
        public ScheduleException(string message) : base(message) { }

        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleException"/> class.
        /// </summary>
        /// <param name="message">The message.</param>
        /// <param name="inner">The inner.</param>
        public ScheduleException(string message, Exception inner) : base(message, inner) { }

        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleException"/> class.
        /// </summary>
        /// <param name="info">The <see cref="T:System.Runtime.Serialization.SerializationInfo" /> that holds the serialized object data about the exception being thrown.</param>
        /// <param name="context">The <see cref="T:System.Runtime.Serialization.StreamingContext" /> that contains contextual information about the source or destination.</param>
        protected ScheduleException(
          System.Runtime.Serialization.SerializationInfo info,
          System.Runtime.Serialization.StreamingContext context) : base(info, context) { }
    }
}
