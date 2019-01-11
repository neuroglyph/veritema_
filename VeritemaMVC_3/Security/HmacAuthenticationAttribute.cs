using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Security.Cryptography;
using System.Security.Principal;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Web;
using System.Web.Http.Filters;
using System.Web.Http.Results;

namespace Veritema.Security
{
    /// <summary>
    /// A <see cref="IAuthenticationFilter"/> which handles an API key
    /// </summary>
    /// <seealso cref="System.Attribute" />
    /// <seealso cref="System.Web.Http.Filters.IAuthenticationFilter" />
    /// <remarks>http://bitoftech.net/2014/12/15/secure-asp-net-web-api-using-api-key-authentication-hmac-authentication/
    /// https://github.com/tjoudeh/WebApiHMACAuthentication</remarks>
    public class HmacAuthenticationAttribute : Attribute, IAuthenticationFilter
    {
        // todo replace with a data store
        private static Dictionary<string, string> allowedApps = CreateTestApiKeyStore();
        private readonly UInt64 requestMaxAgeInSeconds = 300;  //5 mins
        private readonly string authenticationScheme = "amx";

        /// <summary>
        /// Gets or sets a value indicating whether more than one instance of the indicated
        /// attribute can be specified for a single program element.
        /// </summary>
        /// <value><c>false</c>.</value>
        public bool AllowMultiple => false;

        /// <summary>
        /// Authenticates the request.
        /// </summary>
        /// <param name="context">The authentication context.</param>
        /// <param name="cancellationToken">The token to monitor for cancellation requests.</param>
        /// <returns>A Task that will perform authentication.</returns>
        public Task AuthenticateAsync(HttpAuthenticationContext context, CancellationToken cancellationToken)
        {
            var req = context.Request;

            if (req.Headers.Authorization != null && authenticationScheme.Equals(req.Headers.Authorization.Scheme, StringComparison.OrdinalIgnoreCase))
            {
                var rawAuthzHeader = req.Headers.Authorization.Parameter;

                var autherizationHeaderArray = GetAutherizationHeaderValues(rawAuthzHeader);

                if (autherizationHeaderArray != null)
                {
                    var APPId = autherizationHeaderArray[0];
                    var incomingBase64Signature = autherizationHeaderArray[1];
                    var nonce = autherizationHeaderArray[2];
                    var requestTimeStamp = autherizationHeaderArray[3];

                    var isValid = IsValidRequest(req, APPId, incomingBase64Signature, nonce, requestTimeStamp);

                    if (isValid.Result)
                    {
                        var currentPrincipal = new GenericPrincipal(new GenericIdentity(APPId), null);
                        context.Principal = currentPrincipal;
                    }
                    else
                    {
                        context.ErrorResult = new UnauthorizedResult(new AuthenticationHeaderValue[0], context.Request);
                    }
                }
                else
                {
                    context.ErrorResult = new UnauthorizedResult(new AuthenticationHeaderValue[0], context.Request);
                }
            }
            else
            {
                context.ErrorResult = new UnauthorizedResult(new AuthenticationHeaderValue[0], context.Request);
            }

            return Task.FromResult(0);
        }

        /// <summary>
        /// Challenges the asynchronous.
        /// </summary>
        /// <param name="context">The context.</param>
        /// <param name="cancellationToken">The cancellation token.</param>
        /// <returns>Task.</returns>
        public Task ChallengeAsync(HttpAuthenticationChallengeContext context, CancellationToken cancellationToken)
        {
            context.Result = new ResultWithChallenge(context.Result,authenticationScheme);
            return Task.FromResult(0);
        }

        private static async Task<byte[]> ComputeHash(HttpContent httpContent)
        {
            using (MD5 md5 = MD5.Create())
            {
                byte[] hash = null;
                var content = await httpContent.ReadAsByteArrayAsync();
                if (content.Length != 0)
                {
                    hash = md5.ComputeHash(content);
                }
                return hash;
            }
        }

        private static Dictionary<string, string> CreateTestApiKeyStore()
        {
            return new Dictionary<string, string> {
                ["a92c7e2de6cc4d0fa30f86a4be1caecc"] = "ebYRpJkLtEmJ9wLbMqFKskdUVlMNMfYkYKsAQosvFFE="
            };
        }

        private string[] GetAutherizationHeaderValues(string rawAuthzHeader)
        {
            var components = rawAuthzHeader.Split(':');
            return components.Length == 4 ? components : null;
        }


        private bool IsReplayRequest(string nonce, string requestTimeStamp)
        {
            if (System.Runtime.Caching.MemoryCache.Default.Contains(nonce))
            {
                return true;
            }

            DateTime epochStart = new DateTime(1970, 01, 01, 0, 0, 0, 0, DateTimeKind.Utc);
            TimeSpan currentTs = DateTime.UtcNow - epochStart;

            var serverTotalSeconds = Convert.ToUInt64(currentTs.TotalSeconds);
            var requestTotalSeconds = Convert.ToUInt64(requestTimeStamp);

            if ((serverTotalSeconds - requestTotalSeconds) > requestMaxAgeInSeconds)
            {
                return true;
            }

            System.Runtime.Caching.MemoryCache.Default.Add(nonce, requestTimeStamp, DateTimeOffset.UtcNow.AddSeconds(requestMaxAgeInSeconds));

            return false;
        }

        private async Task<bool> IsValidRequest(HttpRequestMessage req, string APPId, string incomingBase64Signature, string nonce, string requestTimeStamp)
        {
            string requestContentBase64String = "";
            string requestUri = HttpUtility.UrlEncode(req.RequestUri.AbsoluteUri.ToLower());
            string requestHttpMethod = req.Method.Method;

            if (!allowedApps.ContainsKey(APPId))
            {
                return false;
            }

            var sharedKey = allowedApps[APPId];

            if (IsReplayRequest(nonce, requestTimeStamp))
            {
                return false;
            }

            byte[] hash = await ComputeHash(req.Content);

            if (hash != null)
            {
                requestContentBase64String = Convert.ToBase64String(hash);
            }

            string data = String.Format("{0}{1}{2}{3}{4}{5}", APPId, requestHttpMethod, requestUri, requestTimeStamp, nonce, requestContentBase64String);

            var secretKeyBytes = Convert.FromBase64String(sharedKey);

            byte[] signature = Encoding.UTF8.GetBytes(data);

            using (HMACSHA256 hmac = new HMACSHA256(secretKeyBytes))
            {
                byte[] signatureBytes = hmac.ComputeHash(signature);

                return (incomingBase64Signature.Equals(Convert.ToBase64String(signatureBytes), StringComparison.Ordinal));
            }

        }

    }
}
