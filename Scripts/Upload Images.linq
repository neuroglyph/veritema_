<Query Kind="Statements">
  <NuGetReference>WindowsAzure.Storage</NuGetReference>
  <Namespace>Microsoft.WindowsAzure.Storage.Blob</Namespace>
  <Namespace>Microsoft.WindowsAzure.Storage</Namespace>
</Query>

CloudStorageAccount account = CloudStorageAccount.Parse("DefaultEndpointsProtocol=https;AccountName=veritema;AccountKey=dNlVjU7FoB3hLlhpcxlQCJ6xKq+Tz5BYcEjg8ti72jHth7rGXiZ+kF+QOXPCZZvCHgIBGOIdg4BqXbTTVxfILg==");
var client = account.CreateCloudBlobClient();
var container = client.GetContainerReference("images");
var block = container.GetBlockBlobReference("small_circle_hapkido_logo.png");
block.Properties.ContentType="image/png";
block.Properties.CacheControl = "public, max-age=31536000";

var bytes = File.ReadAllBytes(@"c:\users\ted_000\desktop\Small Circle Hapkido logo.png");
block.UploadFromByteArray(bytes, 0, bytes.Length);
block.Uri.Dump();

