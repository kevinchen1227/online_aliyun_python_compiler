from aliyunsdkcore.client import AcsClient
from aliyunsdkcore.acs_exception.exceptions import ClientException
from aliyunsdkcore.acs_exception.exceptions import ServerException
from aliyunsdkecs.request.v20140526 import DescribeInstancesRequest
from aliyunsdkecs.request.v20140526 import StopInstanceRequest
client = AcsClient(
    "LTAI5tRzizSiopu3udwZg7gH",
    "php",
    "ap-northeast-1"
);
# Initialize a request and set parameters
request = DescribeInstancesRequest.DescribeInstancesRequest()
request.set_PageSize(10)
# Print response
response = client.do_action_with_exception(request)
print (response)