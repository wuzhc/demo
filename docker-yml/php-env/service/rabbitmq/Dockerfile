FROM rabbitmq
RUN ["rabbitmq-server", "start"]
RUN ["rabbitmq-plugins", "enable", "rabbitmq_management"]
RUN ["rabbitmqctl", "add_user", "admin", "admin123"]
RUN ["rabbitmqtcl", "set_user_tags", "admin", "administrator"]
