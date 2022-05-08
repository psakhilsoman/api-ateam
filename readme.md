
Login : Method = post

http://localhost/api/wp-json/api/v1/authn

{
    email: "ragini@ateamsoftsolutions.com",
    password: "1234567890"
}

------------------------------------------

Register : Method = post


http://localhost/api/wp-json/api/v1/signin

{
  "title": "Ms",
  "firstname": "Ragini",
  "lastname": "Ananthan",
  "email": "ragini@ateamsoftsolutions.com",
  "phone": "+911234567890",
  "password": "1234567890",
  "cfpassword": "1234567890",
  "toc_accept": "1",
  "role": "practitioner",
  "sec_key": "secKey",
  "btn_submit": true
}

----------------------------------------
INSERT INTO `physiob1_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES


{
  "provideruid": "00u6y27tva21PD61z4x7",
  uid": 3432,
  "listings": [
    {
      "title": "Health Sure",
      "address_1": "Pulinkuzhy House",
      "address_2": "Avinissery P.O",
      "location": "14500",
      "postcode": "680306",
      "telephone_1": "07356044678",
      "num_workers": 5,
      "specialization": [
        "Musculoskeletal Physiotherapy",
        "Sports Injury Physiotherapy"
      ],
      "treatments": [
        "Back Pain",
        "Neck Pain"
      ]
    }
  ],
  "btn_submit": true
}

---------------------------------------