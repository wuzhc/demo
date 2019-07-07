# coding=utf-8
__author__ = 'wuzhc'
'''
client端
'''
import socket
import json
import uuid

host = '127.0.0.1'
port = 9503
client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
client.connect((host, port))

n = 0
topic = ['topic_1', 'topic_2', 'topic_3', 'topic_4']
while True:
    data = {
        'method': 'Service.Pop',
        'params': [topic],
        'id': 'xxxx_id' + str(uuid.uuid4())
    }
    client.send((json.dumps(data) + '\n').encode())
    res = client.recv(1024)
    print(res.decode())

    resDeco = json.loads(res.decode())
    if resDeco['error']:
        print('err: ' + resDeco['error'])
        continue

    if resDeco['result']['TTR'] > 0:
        data = {
            'method': 'Service.Ack',
            'params': [resDeco['result']['id']],
            'id': 'xxxx_id' + str(uuid.uuid4())
        }
        client.send((json.dumps(data) + '\n').encode())
        res2 = client.recv(1024)
        resDeco2 = json.loads(res.decode())
        if resDeco2['result']:
            print(resDeco['result']['id'] + ' ack success')
