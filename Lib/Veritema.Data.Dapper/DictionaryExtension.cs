using System.Collections.Generic;

namespace Veritema.Data
{
    /// <summary>
    /// Functional extensions to <see cref="Dictionary{TKey,TValue}"/>
    /// </summary>
    public static class DictionaryExtensions
    {
        /// <summary>
        /// Projects the specified key.
        /// </summary>
        /// <typeparam name="TKey">The type of the t key.</typeparam>
        /// <typeparam name="TValue">The type of the t value.</typeparam>
        /// <param name="dictionary">The dictionary.</param>
        /// <param name="key">The key.</param>
        /// <returns>TValue.</returns>
        public static TValue Project<TKey,TValue>(this Dictionary<TKey,TValue> dictionary, TKey key)
        {
            TValue value;
            dictionary.TryGetValue(key, out value);
            return value;
        }
    }
}
