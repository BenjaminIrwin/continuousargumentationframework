
# Note: This schema is free of foreign key constraints.

create table cells
(
    id       int auto_increment
        primary key,
    matrixid int                                     not null,
    type     varchar(10)                             not null,
    label    varchar(250)                            not null,
    value    varchar(1000) default '0'               not null,
    row      int(5)                                  not null,
    `column` int(5)                                  not null,
    ref      varchar(11)                             not null,
    creation timestamp     default CURRENT_TIMESTAMP not null
);

create table daily_brier_score
(
    questionId   int      null,
    userId       int      null,
    date         datetime null,
    expForecast  double   null,
    gjpoForecast double   null
);

create table debates
(
    id               int auto_increment
        primary key,
    questionId       int                                   null,
    name             varchar(65)                           not null,
    defaultbasevalue double                                not null,
    finalforecast    double                                null,
    participants     varchar(200)                          null,
    typevalue        varchar(150)                          not null,
    open             timestamp   default CURRENT_TIMESTAMP not null,
    close            timestamp                             null,
    lastmodified     timestamp   default CURRENT_TIMESTAMP null,
    lastmodifiedby   varchar(65) default 'Benjamin Irwin'  null
)
    engine = MyISAM;

create table edges
(
    id       int auto_increment
        primary key,
    debateid int              not null,
    sourceid int              not null,
    targetid int              not null,
    value    double default 0 not null
)
    engine = MyISAM;

create table edgesfreeze
(
    id         int auto_increment
        primary key,
    originalid int                                 not null,
    matrixid   int                                 not null,
    debateid   int                                 not null,
    sourceid   int                                 not null,
    targetid   int                                 not null,
    value      int                                 not null,
    creation   timestamp default CURRENT_TIMESTAMP not null
);

create table ghost_node_score
(
    user_id    int    null,
    node_id    int    null,
    base_score double null
);

create table mapping
(
    id       int auto_increment
        primary key,
    userid   int                                 not null,
    debateid int                                 not null,
    matrixid int                                 not null,
    creation timestamp default CURRENT_TIMESTAMP not null
);

create table matrices
(
    id       int auto_increment
        primary key,
    name     varchar(250)                        not null,
    userid   int                                 not null,
    result   int                                 not null,
    creation timestamp default CURRENT_TIMESTAMP not null
);

create table nodes
(
    id         int auto_increment
        primary key,
    debateid   int                                     not null,
    name       text                                    not null,
    type       varchar(30)                             not null,
    typevalue  double                                  null,
    state      varchar(30)                             not null,
    attachment varchar(300)                            not null,
    x          int(5)        default 23                not null,
    y          int(5)        default 359               not null,
    creation   timestamp     default CURRENT_TIMESTAMP not null,
    createdby  varchar(65)                             null,
    modifiedby varchar(1000) default 'Benjamin Irwin'  not null
)
    engine = MyISAM;

create table nodesfreeze
(
    id            int auto_increment
        primary key,
    originalid    int                                 not null,
    matrixid      int                                 not null,
    debateid      int                                 not null,
    name          text                                not null,
    basevalue     double                              not null,
    computedvalue double                              not null,
    type          varchar(30)                         not null,
    typevalue     varchar(150)                        not null,
    state         varchar(30)                         not null,
    attachment    varchar(300)                        not null,
    creation      timestamp default CURRENT_TIMESTAMP not null
);

create table questions
(
    id              int auto_increment
        primary key,
    ownerId         int                                 null,
    name            varchar(256)                        null,
    initialForecast double                              null,
    open            timestamp default CURRENT_TIMESTAMP null,
    close           timestamp                           null,
    outcome         tinyint(1)                          null
);

create table rights
(
    id          int auto_increment
        primary key,
    userid      int                                  not null,
    questionid  int                                  not null,
    accessright varchar(5) default 'r'               not null,
    modified    timestamp                            null,
    created     timestamp  default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;

create table user_debate_scores
(
    userId           int    null,
    debateId         int    null,
    confidence_score double null,
    forecast         double null
);

create table user_node_score
(
    user_id    int    null,
    node_id    int    null,
    base_score double null
);

create table users
(
    id         int auto_increment
        primary key,
    username   varchar(65)                         not null,
    email      varchar(200)                        not null,
    password   varchar(65)                         not null,
    expertise  text                                null,
    brierScore double    default 0                 null,
    creation   timestamp default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;

create table wormholes
(
    id        int auto_increment
        primary key,
    userid    int                                 not null,
    srcdebate int                                 not null,
    dstdebate int                                 null,
    srcnode   int                                 not null,
    dstnode   int                                 null,
    creation  timestamp default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;
