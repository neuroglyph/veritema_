namespace Veritema.Data
{
    /// <summary>
    /// A classification to provide to provide semantics around the session
    /// </summary>
    public enum EventType
    {
        /// <summary>
        /// A group based reocurring session based upon a cirriculum
        /// </summary>
        Class = 'C',
        /// <summary>
        /// One on One instruction with the Master
        /// </summary>
        PrivateLesson = 'P',
        /// <summary>
        /// A group session focus on the application of a few techniques
        /// </summary>
        Seminar = 'S'
    }
}
