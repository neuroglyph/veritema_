using System.Threading;
using System.Threading.Tasks;
using System.Web.Http.ExceptionHandling;
using Microsoft.ApplicationInsights;

namespace Veritema.Diagnostics
{
    /// <summary>
    /// Capture WebAPI errors to AppInsights
    /// </summary>
    /// <seealso cref="System.Web.Http.ExceptionHandling.IExceptionLogger" />
    public class AiExceptionLogger : IExceptionLogger
    {
        /// <summary>
        /// Logs an unhandled exception.
        /// </summary>
        /// <param name="context">The exception logger context.</param>
        /// <param name="cancellationToken">The token to monitor for cancellation requests.</param>
        /// <returns>A task representing the asynchronous exception logging operation.</returns>
        public Task LogAsync(ExceptionLoggerContext context, CancellationToken cancellationToken)
        {
            var ai = new TelemetryClient();
            ai.TrackException(context.Exception);
            return Task.FromResult(0);
        }
    }
}