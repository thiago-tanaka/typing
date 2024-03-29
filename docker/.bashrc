# LARAVEL
alias artclear='php artisan route:clear; php artisan config:clear; php artisan cache:clear'
alias pa="php artisan"
alias ls="ls -lha"
alias c="clear"
force_color_prompt=yes
color_prompt=yes
parse_git_branch() {
    git branch 2>/dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/(\1)/'
}
if [ "$color_prompt" = yes ]; then
    PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[01;31m\]$(parse_git_branch)\[\033[00m\]\$ '
else
    PS1='${debian_chroot:+($debian_chroot)}\u@\h:\w$(parse_git_branch)\$ '
fi
unset color_prompt force_color_prompt

# ~/.bashrc: executed by bash(1) for non-login shells.

# Note: PS1 and umask are already set in /etc/profile. You should not
# need this unless you want different defaults for root.
# PS1='${debian_chroot:+($debian_chroot)}\h:\w\$ '
# umask 022

# You may uncomment the following lines if you want `ls' to be colorized:
export LS_OPTIONS='--color=auto'
#eval "`dircolors`"
alias ls='ls $LS_OPTIONS'
alias ll='ls $LS_OPTIONS -l'
alias l='ls $LS_OPTIONS -lA'
alias watch='npm run watch'
#
# Some more alias to avoid making mistakes:
# alias rm='rm -i'
# alias cp='cp -i'
# alias mv='mv -i'

alias test="php artisan test"

function testsuite {

    if [[ $1 ]]; then
        php artisan test --testsuite=$1
    else
        file=$(cat '/var/www/phpunit.xml')
        suites=$(grep -oP '(?<=testsuite name=").*?(?=">)' <<<$file)

        oldIFS=$IFS
        IFS=$'\n'
        choices=($suites)
        IFS=$oldIFS
        PS3="Choose a test suite: "
        select answer in "${choices[@]}"; do
            for item in "${choices[@]}"; do
                if [[ $item == $answer ]]; then
                    break 2
                fi
            done
        done
        php artisan test --testsuite=$answer
    fi
}
