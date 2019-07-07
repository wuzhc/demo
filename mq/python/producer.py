# coding=utf-8
__author__ = 'wuzhc'
'''
client端
'''
import socket
import json
import random
import uuid

host = '127.0.0.1'
port = 9503
client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
client.connect((host, port))

n = 0
topic = ['topic_1', 'topic_2', 'topic_3', 'topic_4']
while True:
    job = {
        'id': 'xxxx_id' + str(uuid.uuid4()),
        'topic': topic[random.randint(0, len(topic) - 1)],
        'body': 'this is a job',
        'delay': str(random.randint(0, 60)),
        'TTR': str(random.randint(0, 30))
    }
    data = {
        'method': 'Service.Push',
        'params': [job],
        'id': str(uuid.uuid4())
    }
    client.send((json.dumps(data) + '\n').encode())
    res = client.recv(1024)
    print(res.decode())

    n += 1
    if n > 10000:
        break
