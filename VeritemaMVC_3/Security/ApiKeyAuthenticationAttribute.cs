using System;
using System.Net.Http.Headers;
using System.Security.Principal;
using System.Threading;
using System.Threading.Tasks;
using System.Web.Http.Filters;
using System.Web.Http.Results;

namespace Veritema.Security
{

    public class ApiKeyAuthenticationAttribute : Attribute, IAuthenticationFilter
    {
        private readonly string authenticationScheme = "ak";
        private readonly string _appId = "a92c7e2de6cc4d0fa30f86a4be1caecc";
        private readonly string _apiKey = "zJdsNijdiT9IbFVrMUAphcvcts7n2bfgCg9BYOAryUQ=";

        public bool AllowMultiple => false;
        

        public Task AuthenticateAsync(HttpAuthenticationContext context, CancellationToken cancellationToken)
        {
            var req = context.Request;
            
            if (req.Headers.Authorization != null && authenticationScheme.Equals(req.Headers.Authorization.Scheme, StringComparison.OrdinalIgnoreCase))
            {
                if(req.Headers.Authorization.Parameter == _apiKey)
                { 
                    //var isValid = IsValidRequest(req, APPId, incomingBase64Signature, nonce, requestTimeStamp);

                    //if (isValid.Result)
                    //{
                        var currentPrincipal = new GenericPrincipal(new GenericIdentity(_appId), null);
                        context.Principal = currentPrincipal;
                    //}
                    //else
                    //{
                    //    context.ErrorResult = new UnauthorizedResult(new AuthenticationHeaderValue[0], context.Request);
                    //}
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

        public Task ChallengeAsync(HttpAuthenticationChallengeContext context, CancellationToken cancellationToken)
        {
            context.Result = new ResultWithChallenge(context.Result, authenticationScheme);
            return Task.FromResult(0);
        }
    }
}