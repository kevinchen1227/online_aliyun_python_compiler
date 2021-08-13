import oss2

endpoint = 'http://oss-cn-hangzhou.aliyuncs.com' # Suppose that your bucket is in the Hangzhou region.

auth = oss2.Auth('LTAI5tRzizSiopu3udwZg7gH', 'tq6lDDgk5iOsDL1orn9Dk7cvUInJNt')
bucket = oss2.Bucket(auth, endpoint, 'kevin-test')

# The object key in the bucket is story.txt
key = 'story.txt'

# Upload
bucket.put_object(key, 'Ali Baba is a happy youth.')

# Download
bucket.get_object(key).read()

# Delete
bucket.delete_object(key)

# Traverse all objects in the bucket
for object_info in oss2.ObjectIterator(bucket):
    print(object_info.key)