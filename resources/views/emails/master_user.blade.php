@extends('emails.master')

@section('content')
    <tr>
        <td bgcolor="#F4F5F5" style="border-collapse: collapse;">&nbsp;</td>
    </tr>
    <tr>
        <td style="border-collapse: collapse;">
            <table cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFFF" width="600" align="center" class="header">
                <tr>
                    <td class="logo" style="text-align:center;border-collapse: collapse; vertical-align: middle; padding-left:34px; padding-top:20px; padding-bottom:12px" valign="middle">
                        <img src="{{ asset('icons/sbeeh.png') }}" alt="" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="content" style="border-collapse: collapse;">
            <div style="font-size: 18px; margin: 42px 40px 42px; padding: 0;">
                @yield('body')
            </div>
        </td>
    </tr>
@stop

@section('footer')

    <p style="color: #FFFFFF; font-size: 13px; line-height: 18px; margin: 0 0 7px; padding: 0;">
        {{ trans('texts.txt_mail_footer_1') }} <a href="https://sbeeh.io/cgv/">{{ trans('texts.txt_mail_footer_2') }}</a> {{ trans('texts.txt_mail_footer_3') }}.
    </p>

    <p style="color: #FFFFFF; font-size: 13px; line-height: 18px; margin: 0 0 7px; padding: 0;">
        {{ trans('texts.txt_mail_footer_4') }}
        {{ trans('texts.txt_mail_footer_5') }} <a href="mailto:contact@sbeeh.io">contact@sbeeh.io</a>.
    </p>

    <p style="color: #FFFFFF; font-size: 13px; line-height: 18px; margin: 0 0 7px; padding: 0;">
        {{ trans('texts.txt_mail_footer_6') }} <a href="mailto:{{$userInfo['email']}}">{{$userInfo['email']}}</a> {{ trans('texts.txt_mail_footer_7') }}
        {{ trans('texts.txt_mail_footer_8') }}
    </p>
@stop
