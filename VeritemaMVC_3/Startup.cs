using Microsoft.Owin;
using Owin;

[assembly: OwinStartupAttribute(typeof(VeritemaMVC_3.Startup))]
namespace VeritemaMVC_3
{
    public partial class Startup
    {
        public void Configuration(IAppBuilder app)
        {
            ConfigureAuth(app);
        }
    }
}
