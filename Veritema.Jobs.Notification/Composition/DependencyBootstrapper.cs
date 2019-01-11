using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Practices.Unity;
using Veritema.Data;
using Veritema.Notification;

namespace Veritema.Jobs.Notification.Composition
{
    public class DependencyBootstrapper
    {
        /// <summary>
        /// Configures the specified container.
        /// </summary>
        /// <param name="container">The container to be updated.</param>
        public void Configure(IUnityContainer container)
        {
            // register the DAL
            container.RegisterType<IConnectionStringResolver, ConfigurationConnectionStringResolver>();
            container.RegisterType<IEventLoader, DapperEventLoader>();

            // register the notification
            container.RegisterType<INotifier, SmtpNotifier>();
        }
    }
}
