FROM dexodus/php:8.2-fpm-alpine3.17

RUN apk update \
    && apk --no-cache add git zsh postgresql-client

ARG LINUX_USER_ID

RUN addgroup --gid $LINUX_USER_ID docker \
    && adduser --uid $LINUX_USER_ID --ingroup docker --home /home/docker --shell /bin/zsh --disabled-password --gecos "" docker

USER $LINUX_USER_ID

RUN wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh || true
RUN echo 'export ZSH=/home/docker/.oh-my-zsh' > ~/.zshrc \
    && echo 'ZSH_THEME="simple"' >> ~/.zshrc \
    && echo 'plugins=(npm)' >> ~/.zshrc \
    && echo 'source $ZSH/oh-my-zsh.sh' >> ~/.zshrc \
    && echo 'PROMPT="%{$fg_bold[yellow]%}php_cli@docker_monitor %{$fg_bold[blue]%}%(!.%1~.%~)%{$reset_color%} "' > ~/.oh-my-zsh/themes/simple.zsh-theme

COPY docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /srv/app
