FROM rabbitmq
RUN rabbitmq-server start > /dev/null
RUN rabbitmq-plugins enable rabbitmq_management > /dev/null
RUN ["rabbitmqctl", "add_user", "admin", "admin123"]
RUN ["rabbitmqtcl", "set_user_tags", "admin", "administrator"]
