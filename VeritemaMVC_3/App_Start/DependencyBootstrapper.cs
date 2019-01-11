using Microsoft.Practices.Unity;
using Veritema.Data;
using Veritema.Eventing;

namespace VeritemaMVC_3.App_Start
{
    /// <summary>
    /// Builds the graph of type dependencies
    /// </summary>
    public class DependencyBootstrapper
    {

        /// <summary>
        /// Configures the specified container.
        /// </summary>
        /// <param name="container">The container to be updated.</param>
        public void Configure(IUnityContainer container)
        {
            RegisterDalMappings(container);
        }

        /// <summary>
        /// Registers the type mappings used to interact with the backing store.
        /// </summary>
        /// <param name="container">The container to update.</param>
        private void RegisterDalMappings(IUnityContainer container)
        {
            container.RegisterType<IConnectionStringResolver, ConfigurationConnectionStringResolver>();
            container.RegisterType<IEventLoader, DapperEventLoader>();
            container.RegisterType<IEventWriter, DapperEventWriter>();
            container.RegisterType<IEventScrubber, DapperEventScrubber>();

            container.RegisterType<ILocationReader, DapperLocationLoader>();

            // eventing
            container.RegisterType<IEventPublisher, AzureStorageEventPublisher>();
        }
    }
}
