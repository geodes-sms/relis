FROM tomcat:8.5.51-jdk8-openjdk-slim

ARG DOCUMENT_ROOT

# Fixing user file permissions 
ARG USER_ID
ARG GROUP_ID

# Define environment variables
ENV USER_ID=$USER_ID
ENV GROUP_ID=$GROUP_ID
ENV USER_ID=${USER_ID:-1001}
ENV GROUP_ID=${GROUP_ID:-1001}

# Add group and user based on build arguments
RUN addgroup --gid ${GROUP_ID} grpdef
RUN adduser --disabled-password --gecos '' --uid ${USER_ID} --gid ${GROUP_ID} relis

COPY ./relis.war /usr/local/tomcat/webapps/relis.war
COPY ./entrypoint.sh /usr/local/bin
RUN sed 's/\r$//g' /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
USER relis:grpdef
ENTRYPOINT [ "/usr/local/bin/entrypoint.sh" ]