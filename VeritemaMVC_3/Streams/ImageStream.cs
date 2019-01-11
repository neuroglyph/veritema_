using System.IO;
using System.Net;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace ASPNET_Web_Api_SelfHost_VideoStreaming.Streams
{
    internal class ImageStream
    {
        public object Boundary { get; private set; } = "HintDesk";

        public async Task WriteToStream(Stream outputStream, HttpContent content, TransportContext context)
        {
            byte[] newLine = Encoding.UTF8.GetBytes("\r\n");

            foreach (var file in Directory.GetFiles(@"TestData\Images", "*.jpg"))
            {
                var fileInfo = new FileInfo(file);
                var header = $"--{Boundary}\r\nContent-Type: image/jpeg\r\nContent-Length: {fileInfo.Length}\r\n\r\n";
                var headerData = Encoding.UTF8.GetBytes(header);
                await outputStream.WriteAsync(headerData, 0, headerData.Length);
                await fileInfo.OpenRead().CopyToAsync(outputStream);
                await outputStream.WriteAsync(newLine, 0, newLine.Length);
                await Task.Delay(1000 / 30);
            }
        }
    }
}