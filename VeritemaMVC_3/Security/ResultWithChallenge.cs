using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading;
using System.Threading.Tasks;
using System.Web.Http;

namespace Veritema.Security
{
    /// <summary>
    /// The response to add an authentication scheme
    /// </summary>
    /// <seealso cref="System.Web.Http.IHttpActionResult" />
    public class ResultWithChallenge : IHttpActionResult
    {
        private readonly string _authenticationScheme;
        private readonly IHttpActionResult _next;

        /// <summary>
        /// Initializes a new instance of the <see cref="ResultWithChallenge" /> class.
        /// </summary>
        /// <param name="next">The next.</param>
        /// <param name="authenticationScheme">The authentication scheme.</param>
        public ResultWithChallenge(IHttpActionResult next, string authenticationScheme)
        {
            _next = next;
            _authenticationScheme = authenticationScheme;
        }

        /// <summary>
        /// execute as an asynchronous operation.
        /// </summary>
        /// <param name="cancellationToken">The token to monitor for cancellation requests.</param>
        /// <returns>A task that, when completed, contains the <see cref="T:System.Net.Http.HttpResponseMessage" />.</returns>
        public async Task<HttpResponseMessage> ExecuteAsync(CancellationToken cancellationToken)
        {
            var response = await _next.ExecuteAsync(cancellationToken);

            if (response.StatusCode == HttpStatusCode.Unauthorized)
            {
                response.Headers.WwwAuthenticate.Add(new AuthenticationHeaderValue(_authenticationScheme));
            }

            return response;
        }
    }
}